import React from 'react';
import Error from '../../common/components/Error';
import Loader from '../../common/components/Loader';
import ResultLevel from '../../common/components/ResultLevel';
import Title from '../../common/components/Title';
import ShowSiteRuns from '../../run/containers/ShowSiteRuns';
import CheckLevelsDatagrid from '../components/CheckLevelsDatagrid';
import {useSite} from '../hooks';

type Props = {
    id: string,
};

export default function ({id}: Props) {
    const [site, loading, error] = useSite(id);

    return (
        <div>
            <Title>{
                site ? (
                    <>
                        Site {site.name}
                        {site.lastRun && <ResultLevel level={site.lastRun.lowerResultLevel}/>}
                    </>
                ) : ''}
            </Title>

            <Error error={error}/>
            <Loader loading={loading}/>

            {site && (
                <>
                    <Title level={3}>Checks</Title>
                    <CheckLevelsDatagrid
                        levels={
                            Object.keys(site.lastLevelsGroupedByCheckers)
                                .map(check => ({check, level: site.lastLevelsGroupedByCheckers[check]}))
                        } />

                    <Title level={3}>Runs</Title>
                    <ShowSiteRuns siteId={id}/>
                </>
            )}
        </div>
    );
}
