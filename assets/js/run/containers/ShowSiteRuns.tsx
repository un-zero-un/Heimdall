import React from 'react';
import RunDatagrid from '../components/RunDatagrid';
import {useSiteRuns} from '../hooks';

type Props = {
    siteId: string,
};

export default function ShowSiteRuns({siteId}: Props) {
    const runsData = useSiteRuns(siteId);

    return <RunDatagrid runs={'success' === runsData.status ? runsData.data : null}
                        loading={runsData.isLoading}
                        error={runsData.isErrored} />;
}
