export interface DashboardTrend {
    value: number;
    direction: 'up' | 'down';
}

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
    activePositionsTrend: DashboardTrend | null;
    candidatesTrend: DashboardTrend | null;
    interviewsTrend: DashboardTrend | null;
    strongCandidatesTrend: DashboardTrend | null;
    recentInterviews: DashboardInterview[];
    userName: string;
}
