import {DataTable} from 'grommet';
import React from 'react';
import Error from '../../common/components/Error';
import Loader from '../../common/components/Loader';
import ResultLevel from '../../common/components/ResultLevel';
import {Trans} from '../../common/TranslationProvider';
import {RunCheckResultCollection} from '../../types/check';

type Props = {
    runCheckResults: RunCheckResultCollection | null,
    error?: boolean,
    loading?: boolean,
};

export default function RunCheckResultDatagrid({runCheckResults, error = false, loading = false}: Props) {
    return (
        <>
            <Error error={error}/>
            <Loader loading={loading}/>

            {runCheckResults && <DataTable
                columns={[
                    {
                        property: 'type',
                        header:   'Type',
                        render:   runCheckResult => (
                            <Trans params={runCheckResult.data}>{'check_result.type.' + runCheckResult.type}</Trans>
                        ),
                    },
                    {
                        property: 'id',
                        primary:  true,
                        render:   runCheckResult => <ResultLevel level={runCheckResult.level} />,
                    },
                ]}
                data={runCheckResults['hydra:member']}
            />}
        </>
    );
}
