import type { InterviewSession } from '@/types/interview';

/**
 * Presentation-only sample used when no interview is currently in
 * progress so the dashboard always demonstrates the full experience.
 * All real usage flows through the backend-provided session payload.
 */
export function createDemoSession(): InterviewSession {
    return {
        activeInterview: {
            id: 0,
            candidateName: 'David Johnson',
            jobTitle: 'Senior Frontend Developer',
            status: 'in_progress',
            startedAt: new Date().toISOString(),
            durationSeconds: 45 * 60,
            remainingSeconds: 32 * 60 + 14,
        },
        currentQuestion: {
            text: 'Can you explain the difference between let, const, and var in JavaScript?',
            state: 'listening',
        },
        progress: {
            percentage: 60,
            sections: [
                {
                    name: 'Introduction',
                    completed: 5,
                    total: 5,
                    status: 'completed',
                },
                {
                    name: 'Technical Skills',
                    completed: 8,
                    total: 15,
                    status: 'in_progress',
                    children: [
                        {
                            name: 'Frontend (JavaScript)',
                            completed: 3,
                            total: 5,
                            status: 'in_progress',
                        },
                        {
                            name: 'Frameworks (Vue/React)',
                            completed: 2,
                            total: 5,
                            status: 'in_progress',
                        },
                        {
                            name: 'HTML & CSS',
                            completed: 0,
                            total: 5,
                            status: 'pending',
                        },
                    ],
                },
                {
                    name: 'Problem Solving',
                    completed: 0,
                    total: 10,
                    status: 'pending',
                },
                {
                    name: 'System Design',
                    completed: 0,
                    total: 5,
                    status: 'pending',
                },
                {
                    name: 'Behavioral',
                    completed: 0,
                    total: 5,
                    status: 'pending',
                },
                {
                    name: 'Closing',
                    completed: 0,
                    total: 5,
                    status: 'pending',
                },
            ],
        },
        skills: [
            { skill: 'JavaScript', score: 85 },
            { skill: 'Vue/React', score: 80 },
            { skill: 'HTML/CSS', score: 90 },
            { skill: 'Problem Solving', score: 75 },
            { skill: 'Communication', score: 70 },
            { skill: 'System Design', score: 60 },
        ],
        statistics: {
            interviews: { value: 12, trend: { value: 20, direction: 'up' } },
            completed: { value: 8, trend: { value: 33, direction: 'up' } },
            inProgress: { value: 4, trend: { value: 10, direction: 'up' } },
            averageScorePercent: { value: 85, trend: null },
        },
        interviewsOverTime: [
            { label: 'Mon', count: 6 },
            { label: 'Tue', count: 10 },
            { label: 'Wed', count: 17 },
            { label: 'Thu', count: 18 },
            { label: 'Fri', count: 12 },
            { label: 'Sat', count: 11 },
            { label: 'Sun', count: 5 },
        ],
    };
}
