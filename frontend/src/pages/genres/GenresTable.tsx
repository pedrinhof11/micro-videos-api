import React, {useEffect, useState} from "react";
import { Chip } from "@material-ui/core";
import MUIDataTable, { MUIDataTableColumnDef } from "mui-datatables";
import { httpVideo } from "../../http";
import { dateFormatFromIso } from "../../utils";
import { Category } from "../../types/models"

const columns: MUIDataTableColumnDef[] = [
  { name: "name", label: "Nome" },
  { 
    name: "categories", 
    label: "Categorias",
    options: {
      customBodyRender: (categories: Category[]) => {
        return categories.map((value) => value.name).join(", ")
      }
    }
  },
  {
    name: "is_active",
    label: "Ativo?",
    options: {
      customBodyRender: (value: boolean) => {
        return value ? <Chip label="Sim" color="primary" /> : <Chip label="Não" color="secondary" />;
      }
    }
  },
  { 
    name: "created_at",
    label: "Criado em",
    options: {
      customBodyRender: (value, meta) => (
        <span>{dateFormatFromIso(value, 'dd/MM/yyyy')}</span>
      )
    } 
  }
];

const GenresTable = () => {

  const [genres, setGenres] = useState([]);
  
  const fetchData = async () => {
    const {data: {data}} = await httpVideo.get("genres");
    setGenres(data);
  }

  useEffect(() => {
    fetchData()
  },[])


  return (
    <MUIDataTable
    title="Membros do Elenco"
    columns={columns}
    data={genres}
  />
  );
}

export default GenresTable
