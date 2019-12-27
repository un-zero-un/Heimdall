import React from 'react';
import Error from '../../common/components/Error';
import Loader from '../../common/components/Loader';
import ResultLevel from '../../common/components/ResultLevel';
import Title from '../../common/components/Title';
import ShowSiteRuns from '../../run/containers/ShowSiteRuns';
import {ConfiguredCheck} from '../../types/configuredCheck';
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
                        {siteData.data.lastRun && <ResultLevel level={siteData.data.lastRun.siteResult}/>}
                    </>
                ) : ''}
            </Title>

            <Error error={siteData.isErrored}/>
            <Loader loading={siteData.isLoading}/>

            {'success' === siteData.status && (
                <>
                    <Title level={3}>Checks</Title>

                    {siteData.data.configuredChecks && (
                        <CheckLevelsDatagrid
                            levels={
                                siteData.data.configuredChecks
                                    .map((check: ConfiguredCheck) => ({ check: check.check, level: check.lastResult }))
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
