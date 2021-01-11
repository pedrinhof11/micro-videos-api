import React from "react";
import { useParams } from "react-router-dom";
import Page from "../../components/Page";
import CategoriesForm from "./CategoriesForm";

const CategoriesPageForm = () => {
  const { id } = useParams<{ id: string }>();
  return (
    <Page title={!id ? "Criar Categoria" : "Editar Categoria"}>
      <CategoriesForm />
    </Page>
  );
};

export default CategoriesPageForm;
