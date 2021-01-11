import React from "react";
import { useParams } from "react-router-dom";
import Page from "../../components/Page";
import GenresForm from "./GenresForm";

const GenresPageForm = () => {
  const { id } = useParams<{ id: string }>();
  return (
    <Page title={!id ? "Criar Gênero" : "Editar Gênero"}>
      <GenresForm />
    </Page>
  );
};

export default GenresPageForm;
