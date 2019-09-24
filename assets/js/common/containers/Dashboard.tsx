import React from 'react';
import {connect} from 'react-redux'
import {Link} from 'react-router-dom';
import Error from '../../common/components/Error';
import Loader from '../../common/components/Loader';

import Title from '../../common/components/Title';
import {useSites} from "../../site/hooks";

type StateProps = {};
type DispatchProps = {};
type OwnProps = {};
type Props = StateProps & DispatchProps & OwnProps;

function Dashboard({}: Props) {
    const [sites, loading, error] = useSites();

    return (
        <div>
            <Title>Dashboard</Title>

            <Error error={error} />
            <Loader loading={loading} />

            {null !== sites && (
                <table>
                    <tbody>
                    {sites['hydra:member'].map(site => (
                        <tr key={site.id}>
                            <th>
                                <Link to={`/sites/${site.id}`}>{site.name}</Link>
                            </th>
                            <td>
                                {site.lastRun ? site.lastRun.lowerResultLevel : 'N/A'}
                            </td>
                        </tr>
                    ))}
                    </tbody>
                </table>
            )}
        </div>
    )
}

export default connect<StateProps, DispatchProps, OwnProps, {}>(
    () => ({}),
)(Dashboard);
