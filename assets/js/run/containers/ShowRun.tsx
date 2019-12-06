import React from 'react';
import * as Icons from 'grommet-icons';
import ShowRunCheckResults from '../../check/containers/ShowRunCheckResults';
import Error from '../../common/components/Error';
import Loader from '../../common/components/Loader';
import RoutedButton from '../../common/components/RoutedButton';
import Title from '../../common/components/Title';
import RunStatus from '../components/RunStatus';
import {useRun} from '../hooks';

type Props = {
    id: string,
};

export default function ShowRun({id}: Props) {
    const runData = useRun(id);

    return (
        <div>
            <Title>
                Run "{'success' === runData.status && runData.data.site && runData.data.site.name}"
                <RunStatus run={'success' === runData.status ? runData.data : null}/>
            </Title>

            {
                'success' === runData.status &&
                runData.data.site &&
                <RoutedButton path={`/sites/${runData.data.site.id}`} icon={<Icons.Previous/>} label="Back to site"/>
            }

            <Loader loading={runData.isLoading}/>
            <Error error={runData.isErrored}/>

            <ShowRunCheckResults runId={id}/>
        </div>
    );
}
