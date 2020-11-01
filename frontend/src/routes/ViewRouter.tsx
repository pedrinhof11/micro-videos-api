import * as React from 'react';
import {Switch, Route} from "react-router-dom";
import routes from "./index";

type Props = {
    
};
const ViewRouter: React.FC = (props: Props) => {
    return (
        <Switch>
            { routes.map((route, index) => (
                <Route
                    key={index}
                    path={route.path}
                    component={route.component}
                    exact={route.exact === true}
                />
            )) }
        </Switch>
    );
};

export default ViewRouter;