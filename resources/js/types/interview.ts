export type InterviewStatus =
    | 'draft'
    | 'scheduled'
    | 'in_progress'
    | 'paused'
    | 'completed'
    | 'expired'
    | 'cancelled';

export type AiAgentStatus =
    'online' | 'thinking' | 'speaking' | 'listening' | 'processing' | 'offline';

export type RecordingState =
    'recording' | 'paused' | 'processing' | 'completed';

export type QuestionState =
    'pending' | 'asking' | 'listening' | 'processing' | 'answered';

export type SectionStatus = 'completed' | 'in_progress' | 'pending';

/**
 * What the candidate is allowed to know about their own answer's review.
 *
 * Deliberately coarse, and deliberately no score: the conduct page never
 * receives a score, confidence or rubric, so there is nothing here that could
 * leak one. A missing evaluation row is reported as 'pending' rather than
 * inventing a state for it.
 */
export type EvaluationState = 'pending' | 'failed' | 'completed';

export interface ActiveInterview {
    id: number;
    candidateName: string;
    jobTitle: string;
    status: InterviewStatus;
    startedAt: string;
    durationSeconds: number;
    remainingSeconds: number;
}

export interface CurrentQuestion {
    text: string;
    state: QuestionState;
}

export interface InterviewSection {
    name: string;
    completed: number;
    total: number;
    status: SectionStatus;
    children?: InterviewSection[];
}

export interface InterviewProgress {
    percentage: number;
    sections: InterviewSection[];
}

export interface SkillScore {
    skill: string;
    score: number;
}

export type StatTrend = {
    value: number;
    direction: 'up' | 'down';
} | null;

export interface WeeklyStatistic {
    value: number;
    trend: StatTrend;
}

export interface AverageScoreStatistic {
    value: number | null;
    trend: null;
}

export interface InterviewStatistics {
    interviews: WeeklyStatistic;
    completed: WeeklyStatistic;
    inProgress: WeeklyStatistic;
    averageScorePercent: AverageScoreStatistic;
}

export interface DailyInterviewCount {
    label: string;
    count: number;
}

export interface InterviewSession {
    activeInterview: ActiveInterview | null;
    currentQuestion: CurrentQuestion | null;
    progress: InterviewProgress;
    skills: SkillScore[];
    statistics: InterviewStatistics;
    interviewsOverTime: DailyInterviewCount[];
}

export interface ConductInterview {
    id: number;
    status: InterviewStatus;
    startedAt: string | null;
    durationSeconds: number;
    remainingSeconds: number;
    questionIndex: number;
    totalQuestions: number;
    canStart: boolean;
}

export interface ConductCandidate {
    id: number;
    name: string;
}

export interface ConductPosition {
    id: number;
    title: string;
}

export interface ConductQuestion {
    id: number;
    answerId: number | null;
    text: string;
    skill: string;
    difficulty: string;
    status: string;
    evaluationState: EvaluationState;
}

export interface ConductProgress {
    percentage: number;
    answered: number;
    total: number;
    sections: InterviewSection[];
}

export interface ConductPageProps {
    interview: ConductInterview;
    candidate: ConductCandidate;
    position: ConductPosition;
    currentQuestion: ConductQuestion | null;
    progress: ConductProgress;
}
