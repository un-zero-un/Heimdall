import {HasTimestamp, Model} from '../common/types';

export type CheckResultLevel = 'success' | 'warning' | 'error';

export type Check = Model & HasTimestamp & {
    level: CheckResultLevel,
    type: string,
    data: {
        [key: string]: string
    }
};
