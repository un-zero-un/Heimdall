import React from 'react';

type Props = {
    error: boolean,
};

export default function Error({error}: Props) {
    if (!error) {
        return null;
    }

    return <div>Error!</div>;
}
