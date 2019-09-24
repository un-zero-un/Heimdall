import React, {useState} from 'react';
import {Link} from 'react-router-dom';
import Error from '../../common/components/Error';
import Loader from '../../common/components/Loader';
import Title from '../../common/components/Title';
import ShowSiteRuns from '../../run/containers/ShowSiteRuns';
import {useSite} from '../hooks';

type Props = {
    id: string,
};

export default function ({id}: Props) {
    const [site, loading, error]        = useSite(id);
    const [runsVisible, setRunsVisible] = useState(false);

    return (
        <div>
            <Title>{site ? `Site "${site.name}"` : ''}</Title>

            <Error error={error}/>
            <Loader loading={loading}/>

            {site && (
                <div>
                    <div>
                        <h2>Last run results :</h2>

                        {site.lastRun ? (
                            <>
                                {site.lastRun.lowerResultLevel} at {site.lastRun.createdAt}

                                <Link to={`/runs/${site.lastRun.id}`}>Show run</Link>
                            </>
                        ) : 'N/A'}

                    </div>

                    <div>
                        <h2>Last runs</h2>

                        {runsVisible ?
                            <ShowSiteRuns siteId={id}/> :
                            <button onClick={() => setRunsVisible(true)}>Show runs</button>
                        }
                    </div>
                </div>
            )}
        </div>
    );
}
