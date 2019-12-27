import {HasTimestamp, Model, ModelCollection} from '../common/types';
import {ConfiguredCheck} from './configuredCheck';
import {Run} from './run';

export type Site = Model & HasTimestamp & {
    '@type': 'Site',
    name: string,
    slug: string,
    url: string,
    lastRun: Run | null,
    configuredChecks?: ConfiguredCheck[],
}

export type SiteCollection = ModelCollection<Site>;
