import React from 'react';
import {Link} from 'react-router-dom';
import Error from '../../common/components/Error';
import Loader from '../../common/components/Loader';
import Title from '../../common/components/Title';
import {useRun} from '../hooks';

type Props = {
    id: string,
};

export default function ShowRun({id}: Props) {
    const [run, loading, error] = useRun(id);

    return (
        <div>
            <Title>{run ? `Run "${run.site && run.site.name}"` : ''}</Title>

            {run && run.site && <Link to={`/sites/${run.site.id}`}>Back to site</Link>}

            <Loader loading={loading}/>
            <Error error={error}/>

            {null !== run && (
                <table>
                    <thead>
                    <tr>
                        <th>Level</th>
                        <td>Type</td>
                    </tr>
                    </thead>
                    <tbody>
                    {run.checkResults && run.checkResults.map((check, i) => (
                        <tr key={run.id + '-' + i}>
                            <td>{check.level}</td>
                            <td>{check.type}</td>
                        </tr>
                    ))}
                    </tbody>
                </table>
            )}
        </div>
    );
}
