<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\BetRequest;
use App\Player;
use App\Bet;
use App\BetSelections;
use App\BalanceTransaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use App\Repositories\PlayerRepositoryInterface;

class BetController extends Controller
{
    protected $repository;

   
    public function __construct(PlayerRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function getPlayer($id)
    {
        $player = $this->repository->find($id);
        return new JsonResponse($player, JsonResponse::HTTP_CREATED);
    }


    /**
     * save betting details
     * @bodyParam player_id int player id in the system
     * @bodyParam stake_amount string amount of money player wants to bet
     * @bodyParam selections array selection (events) on which player wants to bet
     * @param BetRequest $request
     * @return JsonResponse
     */
    public function store(BetRequest $request)
    {
        $data = $request->all();
        $player = Player::find($data['player_id']);

        if (!$player) {
            // Create player
            $player = Player::create(
                ['id' => $data['player_id'], 'balance' => 1000]
            );
        }
        
        DB::beginTransaction();
        try {
            $balance = $player->balance - $data['stake_amount'];
            // insert bet for the player
            $bet = Bet::create(
                [
                    'stake_amount'=> $data['stake_amount'], 
                    'player_id' => $data['player_id']
                ]
            );
            // insert bet selections 
            $selections = [];
            foreach ($data['selections'] as $selection) {
                $selections[] = [
                    'bet_id' => $bet->id,
                    'selection_id' => $selection['id'],
                    'odds' => $selection['odds']
                ];
            }
            BetSelections::insert($selections);
            // insert balance transaction for the player
            BalanceTransaction::create(
                [
                    'bet_id' => $bet->id,
                    'amount'=> $data['stake_amount'],
                    'amount_before'=> $player->balance,
                    'player_id' => $data['player_id']
                ]
            );
            // update balance for player
            $player->update(['balance' => $balance]);
            DB::commit();
        } catch(\Exception $e) {
            DB::rollBack();
            return new JsonResponse(
                ['errors' => ['code' => 0, 'message' => 'Unknown error']], 
                JsonResponse::HTTP_BAD_REQUEST
            );
        }
        return new JsonResponse(null, JsonResponse::HTTP_CREATED);
    }
}
