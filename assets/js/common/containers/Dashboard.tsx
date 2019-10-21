import React from 'react';

import Title from '../../common/components/Title';
import SiteDatagrid from '../../site/components/SiteDatagrid';
import {useSites} from "../../site/hooks";

type Props = {};

export default function Dashboard({}: Props) {
    const [sites, loading, error] = useSites();

    return (
        <>
            <Title>Dashboard</Title>

            <SiteDatagrid sites={sites} loading={loading} error={error} />
        </>
    );
};
