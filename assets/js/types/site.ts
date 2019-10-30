import {HasTimestamp, Model, ModelCollection} from "../common/types";
import {Run} from "./run";

export type Site = Model & HasTimestamp & {
    '@type': 'Site',
    name: string,
    slug: string,
    url: string,
    lastRun: Run | null,
    lastLevelsGroupedByCheckers: { [check: string]: string },
}

export type SiteCollection = ModelCollection<Site>;
