import React from 'react';
import RunDatagrid from '../components/RunDatagrid';
import {useSiteRuns} from '../hooks';

type Props = {
    siteId: string,
};

export default function ShowSiteRuns({siteId}: Props) {
    const [runs, loading, error] = useSiteRuns(siteId);

    return <RunDatagrid runs={runs} loading={loading} error={error} />;
}
