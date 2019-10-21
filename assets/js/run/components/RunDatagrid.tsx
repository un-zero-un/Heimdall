import {DataTable} from 'grommet';
import * as Icons from 'grommet-icons';
import React from 'react';
import DateDiff from '../../common/components/DateDiff';
import Error from '../../common/components/Error';
import Loader from '../../common/components/Loader';
import ResultLevel from '../../common/components/ResultLevel';
import RoutedButton from '../../common/components/RoutedButton';
import {RunCollection} from '../../types/run';

type Props = {
    runs: RunCollection | null,
    error?: boolean,
    loading?: boolean,
};

export default function RunDatagrid({runs, error = false, loading = false}: Props) {
    return (
        <>
            <Error error={error}/>
            <Loader loading={loading}/>

            {runs && <DataTable
                columns={[
                    {
                        property: 'createdAt',
                        primary:  true,
                        header:   'Date',
                        render:   run => <DateDiff date={run.createdAt} />,
                    },
                    {
                        property: 'lowerResultLevel',
                        header:   'Result',
                        render:   run => <ResultLevel level={run.lowerResultLevel}/>,
                    },
                    {
                        property: '',
                        render:   run => <RoutedButton path={`/runs/${run.id}`} icon={<Icons.View/>}/>,
                    },
                ]}
                data={runs['hydra:member']}
            />}
        </>
    );
}
