<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Bounded AI Context
    |--------------------------------------------------------------------------
    |
    | The interview context handed to the AI must stay small and predictable.
    | These limits are enforced by the context builders so a long interview
    | never grows the prompt without bound. They are deliberately explicit
    | rather than hidden inside the prompts.
    |
    */

    'context' => [
        'max_previous_questions' => 5,
        'max_previous_answers' => 5,
        'max_history_answer_chars' => 1500,
        'max_history_question_chars' => 1000,
        'max_job_description_chars' => 2000,
    ],

    /*
    |--------------------------------------------------------------------------
    | AI Output Limits
    |--------------------------------------------------------------------------
    |
    | Bounds applied to generated questions and their evaluation criteria
    | before anything is persisted. Laravel validates AI output; the AI does
    | not get to decide how much space it is allowed to fill.
    |
    */

    'output' => [
        'max_question_chars' => 1000,
        'max_criteria' => 8,
        'min_criteria' => 1,
        'max_score' => 10,
        'min_score' => 0,
    ],

    /*
    |--------------------------------------------------------------------------
    | Candidate Input Limits
    |--------------------------------------------------------------------------
    |
    | The maximum answer length a candidate may submit, and the amount of
    | answer text forwarded to the evaluation prompt.
    |
    */

    'answer' => [
        'max_chars' => 10000,
        'max_evaluation_chars' => 10000,
    ],

    /*
    |--------------------------------------------------------------------------
    | Provider Timeout
    |--------------------------------------------------------------------------
    |
    | Seconds to wait for a single AI request before treating it as a
    | failure. Retries are handled by the interview lifecycle, not the
    | provider, so a failed call never blocks an interview.
    |
    */

    'provider' => [
        'timeout' => 60,
    ],

];
