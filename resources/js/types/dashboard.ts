export interface DashboardInterview {
    id: number;
    candidate_name: string;
    position_title: string;
    status: string;
    score: number | null;
    recommendation: string | null;
    created_at: string;
}

export interface DashboardData {
    activePositionsCount: number;
    candidatesCount: number;
    interviewsCount: number;
    strongCandidatesCount: number;
    recentInterviews: DashboardInterview[];
    userName: string;
}
