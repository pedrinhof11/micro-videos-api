import * as React from 'react';
import MUIDataTable, { MUIDataTableColumnDef} from "mui-datatables";
import {useEffect, useState} from "react";
import {httpVideo} from "../../http";
import {Chip} from "@material-ui/core";
import { dateFormatFromIso } from '../../utils';

const columns: MUIDataTableColumnDef[] = [
  { name: "name", label: "Nome" },
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
      customBodyRender: (value) => {
      return <span>{dateFormatFromIso(value, 'dd/MM/yyyy')}</span>
      }
    } 
  }
];

export default () => {

  const [categories, setCategories] = useState([])

  useEffect(() => {
    const fetchData = async () => {
      const {data: { data }} = await httpVideo.get("categories");
      setCategories(data);
    }
    fetchData()
  }, [])

  return (
    <MUIDataTable
      title="Categorias"
      columns={columns}
      data={categories}
    />
  );
};