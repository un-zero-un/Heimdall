import {HasTimestamp, Model, ModelCollection} from '../common/types';
import {ConfiguredCheck} from './configuredCheck';
import {Run} from './run';
import {RunCheckResult} from './check';

export type Site = Model & HasTimestamp & {
    '@type': 'Site',
    name: string,
    slug: string,
    url: string,
    lastRun: Run | null,
    lastResults: RunCheckResult[],
    configuredChecks?: ConfiguredCheck[],
}

export type SiteCollection = ModelCollection<Site>;
