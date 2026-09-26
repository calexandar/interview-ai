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
                // Skills are a shared global catalogue, not tenant-owned, so
                // they are looked up by id only. Every requested id must
                // resolve before anything is attached.
                $skills = Skill::whereIn('id', $command->skillIds)->get();

                if ($skills->count() !== count(array_unique($command->skillIds))) {
                    abort(422, 'One or more of the selected skills do not exist.');
                }

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
