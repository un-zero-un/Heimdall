import {HasTimestamp, Model, ModelCollection} from '../common/types';
import {CheckResultLevel} from './check';
import {Site} from './site';

export type Run = Model & HasTimestamp & {
    '@type': 'Run',
    site?: Site,
    running: boolean,
    runResult: CheckResultLevel,
    siteResult: CheckResultLevel,
}

export type RunCollection = ModelCollection<Run>;
