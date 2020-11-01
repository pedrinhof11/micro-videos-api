import {RouteProps} from "react-router-dom";
import Dashboard from "../pages/Dashboard";
import CategoryPage from "../pages/categories/Categories";

export interface appRouteProps extends RouteProps {
    name: string,
    label: string
}

const routes : appRouteProps[] = [
    {
        name: "dashboard",
        label: "Dashboard",
        path: '/',
        component: Dashboard,
        exact: true
    },
    {
        name: "categories",
        label: "Listar Categorias",
        path: '/categories',
        component: CategoryPage,
        exact: true
    },
    {
        name: "categories.create",
        label: "Criar Categoria",
        path: '/categories/create',
        component: CategoryPage,
        exact: true
    }
]

export default routes;