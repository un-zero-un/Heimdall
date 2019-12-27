import {HasTimestamp, Model, ModelCollection} from '../common/types';
import {CheckResultLevel} from './check';

export type ConfiguredCheck = Model & HasTimestamp & {
    '@type': 'ConfiguredCheck',
    check: string,
    lastResult: CheckResultLevel,
}

export type ConfiguredCheckCollection = ModelCollection<ConfiguredCheck>;
