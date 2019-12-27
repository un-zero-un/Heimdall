import {HasTimestamp, Model, ModelCollection} from '../common/types';
import {Run} from './run';

export type CheckResultLevel = 'unknown' | 'success' | 'warning' | 'error';

export type RunCheckResult = Model & HasTimestamp & {
    run: Run,
    level: CheckResultLevel,
    type: string,
    data: {
        [key: string]: string
    }
};

export type RunCheckResultCollection = ModelCollection<RunCheckResult>;
