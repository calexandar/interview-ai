<?php

namespace App\Positions\CreatePosition;

use App\Models\Position;
use App\Models\Skill;
use Illuminate\Support\Facades\DB;

class CreatePositionHandler
{
    public function handle(CreatePosition $command): Position
    {
        return DB::transaction(function () use ($command) {
            $position = Position::create([
                'organization_id' => $command->organizationId,
                'title' => $command->title,
                'description' => $command->description,
                'level' => $command->level,
                'duration_minutes' => $command->durationMinutes,
                'question_count' => $command->questionCount,
                'status' => $command->status,
            ]);

            if ($command->skillIds) {
                $skills = Skill::whereIn('id', $command->skillIds)
                    ->where('organization_id', $command->organizationId)
                    ->get();

                foreach ($skills as $skill) {
                    $position->skills()->attach($skill->id, [
                        'weight' => 5,
                        'required' => true,
                    ]);
                }
            }

            return $position->load('skills');
        });
    }
}
