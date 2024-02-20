<?php

namespace App\Http\Controllers;

use App\Models\Game;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class GameController extends Controller
{
    public function start(Request $request){
        $user = $request->user();
        $game = Game::create(
            [
                'user_id'=>$user->id,
            ]
        );

        return response()->json(
            [
                'status'=>true,
                'message'=>"Game created successfully.",
                'id'=>$game->id
            ]
        );
    }

    public function end(Request $request){
        $user = $request->user();
        $validated = Validator::make($request->all(),
        [
            'id'=>'required|numeric',
            'score'=>'required|numeric|between:0,1000',
        ]);
        if($validated->fails()){
            return response()->json(
                [
                    'status'=>false,
                    'message'=>'Validation errors.',
                    'errors'=>$validated->errors()
                ],
                401);
        }
        $game = Game::where(
            [
                ["id","=",$request->id],
                ["user_id","=",$user->id]
            ]
        )->first();
        if(is_null($game)){
            return response()->json(
                [
                    'status'=>false,
                    'message'=>'Game is not found.'
                ],
                404);
        }
        if(!is_null($game->score)){
            return response()->json(
                [
                    'status'=>false,
                    'message'=>'This game is already ended with score of ' . $game->score,
                ],
                400);
        }

        $game->score = $request->score;
        $game->save();

        $bestScore = $user->games()->max('score');
        $playerPosition = Game::whereDate('created_at', Carbon::today())
            ->where('score', '>', $bestScore)
            ->groupBy('user_id') 
            ->selectRaw('COUNT(DISTINCT user_id) as position')
            ->pluck('position')
            ->first() + 1;

        return response()->json(
            [
                'status'=>true,
                'message'=>"Game finished successfully.",
                'game'=>$game,
                'best_score_today'=>$bestScore,
                'leaderboard_position_today'=>$playerPosition
            ]
        );
    }

    public function leaderboard(Request $request){
        $leaderboard = Game::select('user_id', DB::raw('MAX(score) as max_score'))
            ->whereDate('created_at', Carbon::today())
            ->groupBy('user_id')
            ->orderByDesc('max_score')
            ->limit(100)
            ->get();

            $leaderboard->load(['user' => function ($query) {
                $query->select('id', 'email', 'name', 'surname');
            }]);
            
            // Replace user_id with user object
            $leaderboard->transform(function ($item) {
                $item->user = $item->user;
                unset($item->user_id);
                return $item;
            });
        return response()->json($leaderboard);
    }
}
