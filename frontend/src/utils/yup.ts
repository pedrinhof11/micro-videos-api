/* eslint-disable no-template-curly-in-string */
import { setLocale } from "yup";

const ptBR = {
  mixed: {
    required: "O campo ${path} é requerido",
  },
};

setLocale(ptBR);

export * as yup from "yup";
