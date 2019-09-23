import React from 'react';
import {connect} from 'react-redux'

import Title from '../../common/components/Title';
import {useSites} from "../hooks";

type StateProps = {};
type DispatchProps = {};
type OwnProps = {};
type Props = StateProps & DispatchProps & OwnProps;

function Dashboard({}: Props) {
    const [sites, loading, error] = useSites();

    return (
        <div>
            <Title>Dashboard</Title>

            {loading && <div>Chargement...</div>}
            {error && <div>Erreur !</div>}

            {null !== sites && (
                <table>
                    <tbody>
                    {sites['hydra:member'].map(site => (
                        <tr key={site.id}>
                            <th>{site.name}</th>
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
