import {HasTimestamp, Model, ModelCollection} from '../common/types';

export type Run = Model & HasTimestamp & {
    '@type': 'Run',
    lowerResultLevel: 'success' | 'warning' | 'error',
    checkResults?: string[],
}

export type RunCollection = ModelCollection<Run>;
