import moment from 'moment';
import React, {useEffect, useState} from 'react';

type Props = {
    date: Date | string,
};

export default function DateDiff({date}: Props) {
    const [state, setState] = useState<boolean>(false);
    const parsedDate        = date instanceof Date ? date : new Date(Date.parse(date));

    useEffect(() => {
        const timeout = setTimeout(() => setState(!state), 10000);

        return () => clearTimeout(timeout);
    });

    return (
        <time dateTime={parsedDate.toISOString()}>
            <abbr title={moment(date).format('MMMM Do YYYY, h:mm:ss a')}>
                {moment(date).fromNow()}
            </abbr>
        </time>
    );
}
