import React from 'react';
import {Link} from 'react-router-dom';
import Error from '../../common/components/Error';
import Loader from '../../common/components/Loader';
import {useSiteRuns} from '../hooks';

type Props = {
    siteId: string,
};

export default function ShowSiteRuns({siteId}: Props) {
    const [runs, loading, error] = useSiteRuns(siteId);

    return (
        <div>

            <Error error={error}/>
            <Loader loading={loading}/>

            {null !== runs && (
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Result</th>
                            <th>&nbsp;</th>
                        </tr>
                    </thead>
                    <tbody>
                        {runs['hydra:member'].map(run => (
                            <tr key={run.id}>
                                <th>{run.createdAt}</th>
                                <td>{run.lowerResultLevel}</td>
                                <td>
                                    <Link to={`/runs/${run.id}`}>Show run</Link>
                                </td>
                            </tr>
                        ))}
                    </tbody>
                </table>
            )}

        </div>
    );
}
