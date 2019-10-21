import React from 'react';
import Error from '../../common/components/Error';
import Loader from '../../common/components/Loader';
import ResultLevel from '../../common/components/ResultLevel';
import Title from '../../common/components/Title';
import ShowSiteRuns from '../../run/containers/ShowSiteRuns';
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
                        {site.lastRun && <ResultLevel level={site.lastRun.lowerResultLevel} />}
                    </>
                ) : ''}
            </Title>

            <Error error={error}/>
            <Loader loading={loading}/>

            {site && <ShowSiteRuns siteId={id}/> }
        </div>
    );
}
