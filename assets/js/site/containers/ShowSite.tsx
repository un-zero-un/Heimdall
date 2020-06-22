import React from 'react';
import Error from '../../common/components/Error';
import Loader from '../../common/components/Loader';
import ResultLevel from '../../common/components/ResultLevel';
import Title from '../../common/components/Title';
import ShowSiteRuns from '../../run/containers/ShowSiteRuns';
import CheckLevelsDatagrid from '../components/CheckLevelsDatagrid';
import {useSite} from '../hooks';
import {RunCheckResult} from '../../types/check';

type Props = {
    id: string,
};

export default function ({id}: Props) {
    const siteData = useSite(id);

    if ('success' === siteData.status) {
        console.log(siteData.data.lastResults
            .map((result: RunCheckResult) => ({ check: result.configuredCheck?.check || '', level: result.type })));
    }

    return (
        <div>
            <Title>{
                'success' === siteData.status ? (
                    <>
                        Site {siteData.data.name}
                        {siteData.data.lastRun && <ResultLevel level={siteData.data.lastRun.siteResult}/>}
                    </>
                ) : ''}
            </Title>

            <Error error={siteData.isErrored}/>
            <Loader loading={siteData.isLoading}/>

            {'success' === siteData.status && (
                <>
                    <Title level={3}>Checks</Title>

                    {siteData.data.lastResults && (
                        <CheckLevelsDatagrid
                            levels={
                                siteData.data.lastResults
                                    .map((result: RunCheckResult) => ({ check: result.configuredCheck?.check || '', level: result.level }))
                            }
                        />
                    )}

                    <Title level={3}>Runs</Title>
                    <ShowSiteRuns siteId={id}/>
                </>
            )}
        </div>
    );
}
