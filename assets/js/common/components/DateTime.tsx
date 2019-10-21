import moment from 'moment';
import React from 'react';

type Props = {
    date: Date | string,
    format?: string,
};

export default function DateTime({date, format}: Props) {
    const parsedDate = date instanceof Date ? date : new Date(Date.parse(date));

    return <time dateTime={parsedDate.toISOString()}>{moment(date).format(format)}</time>;
}
