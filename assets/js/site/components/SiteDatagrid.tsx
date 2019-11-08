import {DataTable} from 'grommet';
import * as Icons from 'grommet-icons';
import React from 'react';
import DateDiff from '../../common/components/DateDiff';
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
                        property: 'lastRun.createdAt',
                        header: 'Last run',
                        render:   site => site.lastRun ? <DateDiff date={site.lastRun.createdAt} /> : null,
                    },
                    {
                        property: 'lastRun.currentLowerResultLevel',
                        header:   'Current status',
                        render:   site => {
                            if (site.lastRun && site.lastRun.running) {
                                return <Icons.FormRefresh />;
                            }

                            return <ResultLevel level={site.currentLowerResultLevel} />;
                        }
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
