import {HasTimestamp, Model, ModelCollection} from '../common/types';
import {Check, CheckResultLevel} from './check';
import {Site} from './site';

export type Run = Model & HasTimestamp & {
    '@type': 'Run',
    lowerResultLevel: CheckResultLevel,
    checkResults?: Check[],
    site?: Site,
    running: boolean,
}

export type RunCollection = ModelCollection<Run>;
