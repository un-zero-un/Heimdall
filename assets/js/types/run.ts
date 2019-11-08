import {HasTimestamp, Model, ModelCollection} from '../common/types';
import {CheckResultLevel} from './check';
import {Site} from './site';CheckResultLevel

export type Run = Model & HasTimestamp & {
    '@type': 'Run',
    lowerResultLevel: CheckResultLevel,
    site?: Site,
    running: boolean,
}

export type RunCollection = ModelCollection<Run>;
