import React from 'react';

type Props = {
    loading: boolean,
};

export default function Loader({loading}: Props) {
    if (!loading) {
        return null;
    }

    return <div>Loading...</div>;
}
