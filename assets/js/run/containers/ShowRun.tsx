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
    const [run, loading, error] = useRun(id);

    return (
        <div>
            <Title>
                Run "{run && run.site && run.site.name}"
                <RunStatus run={run} />
            </Title>

            {run && run.site && <RoutedButton path={`/sites/${run.site.id}`} icon={<Icons.Previous />} label="Back to site" />}

            <Loader loading={loading}/>
            <Error error={error}/>

            <ShowRunCheckResults runId={id} />
        </div>
    );
}
