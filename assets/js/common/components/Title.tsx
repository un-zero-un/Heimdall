import React, {ReactNode} from 'react';

type Props = {
    children: ReactNode,
    level?: number,
}

export default function Title({children, level = 2}: Props) {
    switch (level) {
        case 2:
            return <h2>{children}</h2>;
        case 3:
            return <h3>{children}</h3>;
        case 4:
            return <h4>{children}</h4>;
    }


    return <div className="title">{children}</div>;
}
