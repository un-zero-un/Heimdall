import {HasTimestamp, Model} from "../common/types";

export type Run = Model & HasTimestamp & {
    '@type': 'Run',
    lowerResultLevel: 'success' | 'warning' | 'error',
}
