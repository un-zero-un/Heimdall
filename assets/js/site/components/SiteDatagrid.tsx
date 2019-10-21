import {DataTable} from 'grommet';
import * as Icons from 'grommet-icons';
import React from 'react';
import Error from '../../common/components/Error';
import Loader from '../../common/components/Loader';
import ResultLevel from '../../common/components/ResultLevel';
import RoutedButton from '../../common/components/RoutedButton';
import {SiteCollection} from '../../types/site';

type Props = {
    sites: SiteCollection | null,
    error?: boolean,
    loading?: boolean,
};

export default function SiteDatagrid({sites, error = false, loading = false}: Props) {
    return (
        <>
            <Error error={error}/>
            <Loader loading={loading}/>

            {sites && <DataTable
                columns={[
                    {
                        property: 'name',
                        primary:  true,
                        header:   'Site',
                    },
                    {
                        property: 'lastRun',
                        header:   'Last result',
                        render:   site => site.lastRun ? <ResultLevel level={site.lastRun.lowerResultLevel} /> : null,
                    },
                    {
                        property: '',
                        render:   site => <RoutedButton path={`/sites/${site.id}`} icon={<Icons.View/>}/>,
                    },
                ]}
                data={sites['hydra:member']}
            />}
        </>
    );
}
