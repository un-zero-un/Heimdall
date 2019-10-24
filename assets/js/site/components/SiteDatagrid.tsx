import {DataTable} from 'grommet';
import * as Icons from 'grommet-icons';
import React from 'react';
import DateDiff from '../../common/components/DateDiff';
import Error from '../../common/components/Error';
import Loader from '../../common/components/Loader';
import RoutedButton from '../../common/components/RoutedButton';
import RunStatus from '../../run/components/RunStatus';
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
                        property: 'lastRun.createdAt',
                        header: 'Last run',
                        render:   site => site.lastRun ? <DateDiff date={site.lastRun.createdAt} /> : null,
                    },
                    {
                        property: 'lastRun.lowerResultLevel',
                        header:   'Last result',
                        render:   site => <RunStatus run={site.lastRun} />
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
