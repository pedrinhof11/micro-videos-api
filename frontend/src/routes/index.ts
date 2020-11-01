import {RouteProps} from "react-router-dom";
import Dashboard from "../pages/Dashboard";
import Categories from "../pages/categories/Categories";
import CastMembers from "../pages/castMembers/CastMembers";
import Genres from "../pages/genres/Genres";

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
        component: Categories,
        exact: true
    },
    {
        name: "castMembers",
        label: "Listar Membros do Elenco",
        path: '/cast-members',
        component: CastMembers,
        exact: true
    },
    {
        name: "genres",
        label: "Listar Gêneros",
        path: '/genres',
        component: Genres,
        exact: true
    }
]

export default routes;