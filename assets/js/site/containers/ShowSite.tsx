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
    const siteData = useSite(id);

    return (
        <div>
            <Title>{
                'success' === siteData.status ? (
                    <>
                        Site {siteData.data.name}
                        {siteData.data.lastRun && <ResultLevel level={siteData.data.lastRun.lowerResultLevel}/>}
                    </>
                ) : ''}
            </Title>

            <Error error={siteData.isErrored}/>
            <Loader loading={siteData.isLoading}/>

            {'success' === siteData.status && (
                <>
                    <Title level={3}>Checks</Title>
                    <CheckLevelsDatagrid
                        levels={
                            Object.keys(siteData.data.lastLevelsGroupedByCheckers)
                                .map(check => ({check, level: siteData.data.lastLevelsGroupedByCheckers[check]}))
                        } />

                    <Title level={3}>Runs</Title>
                    <ShowSiteRuns siteId={id}/>
                </>
            )}
        </div>
    );
}
