import {RouteProps} from "react-router-dom";
import Dashboard from "../pages/Dashboard";
import Categories from "../pages/categories/Categories";
import CastMembers from "../pages/castMembers/CastMembers";
import Genres from "../pages/genres/Genres";
import CategoriesPageForm from "../pages/categories/CategoriesPageForm";
import CastMembersPageForm from "../pages/castMembers/CastMembersPageForm";
import GenresPageForm from "../pages/genres/GenresPageForm";

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
        name: "categories.create",
        label: "Criar Categoria",
        path: '/categories/create',
        component: CategoriesPageForm,
        exact: true
    },
    {
        name: "castMembers",
        label: "Listar Membros de Elenco",
        path: '/cast-members',
        component: CastMembers,
        exact: true
    },
    {
        name: "castMembers.create",
        label: "Criar Membro de Elenco",
        path: '/cast-members/create',
        component: CastMembersPageForm,
        exact: true
    },
    {
        name: "genres",
        label: "Listar Gêneros",
        path: '/genres',
        component: Genres,
        exact: true
    },
    {
        name: "genres.create",
        label: "Criar Gênero",
        path: '/genres/create',
        component: GenresPageForm,
        exact: true
    },
]

export default routes;