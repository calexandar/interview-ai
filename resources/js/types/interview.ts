export type InterviewStatus =
    | 'scheduled'
    | 'in_progress'
    | 'paused'
    | 'completed'
    | 'cancelled';

export type AiAgentStatus =
    | 'online'
    | 'thinking'
    | 'speaking'
    | 'listening'
    | 'processing'
    | 'offline';

export type RecordingState =
    | 'recording'
    | 'paused'
    | 'processing'
    | 'completed';

export type QuestionState =
    | 'pending'
    | 'asking'
    | 'listening'
    | 'processing'
    | 'answered';

export type SectionStatus = 'completed' | 'in_progress' | 'pending';

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
