import { RouteProps } from "react-router-dom";
import CastMembers from "../pages/castMembers/CastMembers";
import CastMembersPageForm from "../pages/castMembers/CastMembersPageForm";
import Categories from "../pages/categories/Categories";
import CategoriesPageForm from "../pages/categories/CategoriesPageForm";
import Dashboard from "../pages/Dashboard";
import Genres from "../pages/genres/Genres";
import GenresPageForm from "../pages/genres/GenresPageForm";

export interface appRouteProps extends RouteProps {
  name: string;
  label: string;
}

const routes: appRouteProps[] = [
  {
    name: "dashboard",
    label: "Dashboard",
    path: "/",
    component: Dashboard,
    exact: true,
  },
  {
    name: "categories",
    label: "Listar Categorias",
    path: "/categories",
    component: Categories,
    exact: true,
  },
  {
    name: "categories.create",
    label: "Criar Categoria",
    path: "/categories/create",
    component: CategoriesPageForm,
    exact: true,
  },
  {
    name: "categories.edit",
    label: "Editar Categoria",
    path: "/categories/:id/edit",
    component: CategoriesPageForm,
    exact: true,
  },
  {
    name: "castMembers",
    label: "Listar Membros de Elenco",
    path: "/cast-members",
    component: CastMembers,
    exact: true,
  },
  {
    name: "castMembers.create",
    label: "Criar Membro de Elenco",
    path: "/cast-members/create",
    component: CastMembersPageForm,
    exact: true,
  },
  {
    name: "castMembers.edit",
    label: "Editar Membro de Elenco",
    path: "/cast-members/:id/edit",
    component: CastMembersPageForm,
    exact: true,
  },
  {
    name: "genres",
    label: "Listar Gêneros",
    path: "/genres",
    component: Genres,
    exact: true,
  },
  {
    name: "genres.create",
    label: "Criar Gênero",
    path: "/genres/create",
    component: GenresPageForm,
    exact: true,
  },
  {
    name: "genres.edit",
    label: "EditarGênero",
    path: "/genres/:id/edit",
    component: GenresPageForm,
    exact: true,
  },
];

export default routes;
