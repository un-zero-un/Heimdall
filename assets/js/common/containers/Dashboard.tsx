import React from 'react';

import Title from '../../common/components/Title';
import SiteDatagrid from '../../site/components/SiteDatagrid';
import {useSites} from "../../site/hooks";

type Props = {};

export default function Dashboard({}: Props) {
    const sitesData = useSites();

    return (
        <>
            <Title>Dashboard</Title>

            <SiteDatagrid
                sites={'success' === sitesData.status ? sitesData.data : null}
                loading={sitesData.isLoading}
                error={sitesData.isErrored} />
        </>
    );
};
