import {DataTable} from 'grommet';
import React from 'react';
import ResultLevel from '../../common/components/ResultLevel';
import {Trans} from '../../common/TranslationProvider';

type Props = {
    levels: { check: string, level: string }[],
};

export default function CheckLevelsDatagrid({levels}: Props) {
    return (
        <DataTable
            columns={[
                {
                    property: 'check',
                    primary:  true,
                    header:   'Check',
                    render: level => <Trans>{`check_result.checker.${level.check}`}</Trans>
                },
                {
                    property: 'level',
                    header:   'Status',
                    render:   level => <ResultLevel level={level.level} />,
                },
            ]}
            data={levels}
        />
    );
}
